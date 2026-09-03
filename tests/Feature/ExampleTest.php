<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMember;
use App\Notifications\TaskWorkspaceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    private function workspaceFor(User $user, string $name = 'Acme'): Workspace
    {
        $workspace = Workspace::create(['name' => $name, 'slug' => strtolower($name).'-'.uniqid(), 'owner_id' => $user->id]);
        WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $user->id, 'role' => 'owner', 'status' => 'active']);

        return $workspace;
    }

    private function inWorkspace(User $user, Workspace $workspace): self
    {
        return $this->actingAs($user)->withSession(['current_workspace_id' => $workspace->id]);
    }

    public function test_registration_requires_workspace_onboarding(): void
    {
        $this->post(route('register'), ['name' => 'New', 'email' => 'new@example.test', 'password' => 'password123', 'password_confirmation' => 'password123'])
            ->assertRedirect(route('workspaces.create'));
        $this->post(route('workspaces.store'), ['name' => 'New workspace'])->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('workspace_members', ['user_id' => User::firstWhere('email', 'new@example.test')->id, 'role' => 'owner']);
    }

    public function test_workspace_scope_blocks_direct_ids_and_manipulated_relationships(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $wa = $this->workspaceFor($a, 'A');
        $wb = $this->workspaceFor($b, 'B');
        $this->inWorkspace($a, $wa)->post(route('clients.store'), ['name' => 'Private A'])->assertRedirect(route('clients'));
        $client = Client::firstOrFail();
        $this->inWorkspace($b, $wb)->get(route('clients.show', $client))->assertNotFound();
        $this->inWorkspace($b, $wb)->post(route('projects.store'), ['name' => 'Attack', 'client_id' => $client->id, 'deadline' => now()->addDay()->toDateString()])->assertSessionHasErrors('client_id');
        $this->assertDatabaseMissing('projects', ['name' => 'Attack']);
    }

    public function test_member_can_only_switch_to_a_member_workspace(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $wa = $this->workspaceFor($a, 'A');
        $wb = $this->workspaceFor($b, 'B');
        $this->inWorkspace($a, $wa)->post(route('workspaces.switch', $wb))->assertForbidden();
    }

    public function test_owner_can_manage_workspace_data_and_membership(): void
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();
        $workspace = $this->workspaceFor($owner);
        WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $invitee->id, 'role' => 'admin', 'status' => 'active']);
        $this->assertDatabaseHas('workspace_members', ['workspace_id' => $workspace->id, 'user_id' => $invitee->id, 'role' => 'admin']);
        $this->inWorkspace($owner, $workspace)->post(route('clients.store'), ['name' => 'Northstar'])->assertRedirect(route('clients'));
        $client = Client::firstOrFail();
        $this->inWorkspace($owner, $workspace)->post(route('projects.store'), ['name' => 'Launch', 'client_id' => $client->id, 'deadline' => now()->addDay()->toDateString()])->assertRedirect(route('projects'));
        $project = Project::firstOrFail();
        $this->inWorkspace($owner, $workspace)->post(route('tasks.store'), ['name' => 'Ship', 'project_id' => $project->id, 'assignee_id' => $invitee->id, 'priority' => 'High', 'status' => 'Todo'])->assertRedirect(route('tasks'));
        $this->assertDatabaseHas('tasks', ['workspace_id' => $workspace->id, 'name' => 'Ship']);
    }

    public function test_password_reset_updates_credentials_and_rejects_bad_token(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.test']);
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        $token = Password::broker()->createToken($user);
        $this->post(route('password.update'), ['email' => $user->email, 'token' => 'invalid', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertSessionHasErrors('email');
        $this->post(route('password.update'), ['email' => $user->email, 'token' => $token, 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect(route('login'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'new-password'])->assertRedirect(route('dashboard'));
    }

    public function test_invitation_is_email_bound_single_use_and_workspace_scoped(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $attacker = User::factory()->create();
        $workspace = $this->workspaceFor($owner);
        $token = str_repeat('a', 64);
        $invite = WorkspaceInvitation::create(['workspace_id' => $workspace->id, 'email' => $recipient->email, 'role' => 'member', 'token_hash' => hash('sha256', $token), 'expires_at' => now()->addHour(), 'invited_by' => $owner->id]);
        $this->actingAs($attacker)->post(route('invitations.accept', $token))->assertForbidden();
        $this->actingAs($recipient)->post(route('invitations.accept', $token))->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('workspace_members', ['workspace_id' => $workspace->id, 'user_id' => $recipient->id]);
        $this->actingAs($recipient)->post(route('invitations.accept', $token))->assertForbidden();
        $this->assertNotNull($invite->fresh()->accepted_at);
    }

    public function test_expired_invitation_and_member_admin_actions_are_rejected(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $other = User::factory()->create();
        $workspace = $this->workspaceFor($owner);
        WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $member->id, 'role' => 'member', 'status' => 'active']);
        $invite = WorkspaceInvitation::create(['workspace_id' => $workspace->id, 'email' => $other->email, 'role' => 'member', 'token_hash' => hash('sha256', 'expired'), 'expires_at' => now()->subMinute(), 'invited_by' => $owner->id]);
        $this->actingAs($other)->post(route('invitations.accept', 'expired'))->assertForbidden();
        $this->inWorkspace($member, $workspace)->post(route('team.invitations.store'), ['email' => $other->email, 'role' => 'member'])->assertForbidden();
        $this->inWorkspace($member, $workspace)->delete(route('team.destroy', $workspace->members()->first()))->assertForbidden();
        $this->assertNull($invite->fresh()->accepted_at);
    }

    public function test_owner_is_protected_and_admin_cannot_change_roles(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $workspace = $this->workspaceFor($owner);
        $adminMembership = WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $admin->id, 'role' => 'admin', 'status' => 'active']);
        $memberMembership = WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $member->id, 'role' => 'member', 'status' => 'active']);
        $this->inWorkspace($admin, $workspace)->put(route('team.update', $memberMembership), ['role' => 'admin'])->assertForbidden();
        $this->inWorkspace($owner, $workspace)->delete(route('team.destroy', $workspace->members()->where('role', 'owner')->first()))->assertForbidden();
        $this->inWorkspace($owner, $workspace)->put(route('team.update', $adminMembership), ['role' => 'member'])->assertRedirect(route('team'));
    }

    public function test_admin_can_invite_member_but_cannot_invite_admin_or_manage_foreign_member(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $target = User::factory()->create();
        $foreignOwner = User::factory()->create();
        $workspace = $this->workspaceFor($owner);
        $foreign = $this->workspaceFor($foreignOwner, 'Foreign');
        WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $admin->id, 'role' => 'admin', 'status' => 'active']);
        $this->inWorkspace($admin, $workspace)->post(route('team.invitations.store'), ['email' => $target->email, 'role' => 'member'])->assertSessionHas('success');
        $this->inWorkspace($admin, $workspace)->post(route('team.invitations.store'), ['email' => $target->email, 'role' => 'admin'])->assertForbidden();
        $this->inWorkspace($admin, $workspace)->delete(route('team.destroy', $foreign->members()->first()))->assertNotFound();
    }

    public function test_password_reset_rejects_expired_token_and_security_headers_are_present(): void
    {
        $user = User::factory()->create(['email' => 'expired@example.test']);
        $token = Password::broker()->createToken($user);
        DB::table('password_reset_tokens')->where('email', $user->email)->update(['created_at' => now()->subHours(2)]);
        $this->post(route('password.update'), ['email' => $user->email, 'token' => $token, 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertSessionHasErrors('email');
        $this->get('/up')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_hidden_owner_fields_and_cross_workspace_task_status_are_ignored_or_rejected(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $wa = $this->workspaceFor($a, 'Workspace A');
        $wb = $this->workspaceFor($b, 'Workspace B');
        $this->inWorkspace($a, $wa)->post(route('clients.store'), ['name' => 'A client', 'workspace_id' => $wb->id, 'user_id' => $b->id])->assertRedirect(route('clients'));
        $client = Client::firstOrFail();
        $this->assertSame($wa->id, $client->workspace_id);
        $task = new Task(['name' => 'B task', 'status' => 'Todo']);
        $task->forceFill(['workspace_id' => $wb->id, 'user_id' => $b->id])->save();
        $this->inWorkspace($a, $wa)->patchJson(route('tasks.updateStatus', $task), ['status' => 'Completed'])->assertNotFound();
        $this->inWorkspace($a, $wa)->get(route('search', ['q' => 'B task']))->assertDontSeeText('B task');
    }

    public function test_notifications_are_private_and_reset_token_cannot_be_reused(): void
    {
        $a = User::factory()->create(['email' => 'private-a@example.test']);
        $b = User::factory()->create();
        $wa = $this->workspaceFor($a);
        $task = new Task(['name' => 'Private task']);
        $task->forceFill(['workspace_id' => $wa->id, 'user_id' => $a->id])->save();
        $a->notify(new TaskWorkspaceNotification($task, 'assigned', 'Private notification'));
        $notification = $a->unreadNotifications()->firstOrFail();
        $this->inWorkspace($b, $this->workspaceFor($b, 'B'))->patch(route('notifications.read', $notification->id))->assertNotFound();
        Auth::logout();
        $token = Password::broker()->createToken($a);
        $payload = ['email' => $a->email, 'token' => $token, 'password' => 'changed-password', 'password_confirmation' => 'changed-password'];
        $this->post(route('password.update'), $payload)->assertRedirect(route('login'));
        $this->post(route('password.update'), $payload)->assertSessionHasErrors('email');
        $this->post(route('login'), ['email' => $a->email, 'password' => 'password'])->assertSessionHasErrors('email');
    }

    public function test_notifications_are_scoped_to_the_active_workspace_for_listing_and_mutation(): void
    {
        $user = User::factory()->create();
        $workspaceA = $this->workspaceFor($user, 'A');
        $workspaceB = $this->workspaceFor($user, 'B');
        $taskA = new Task(['name' => 'Task A']);
        $taskA->forceFill(['workspace_id' => $workspaceA->id, 'user_id' => $user->id])->save();
        $taskB = new Task(['name' => 'Task B']);
        $taskB->forceFill(['workspace_id' => $workspaceB->id, 'user_id' => $user->id])->save();
        $user->notify(new TaskWorkspaceNotification($taskA, 'assigned', 'Workspace A notification'));
        $user->notify(new TaskWorkspaceNotification($taskB, 'assigned', 'Workspace B notification'));
        $notificationA = $user->notifications()->get()->firstWhere('data.task_id', $taskA->id);
        $notificationB = $user->notifications()->get()->firstWhere('data.task_id', $taskB->id);
        $this->assertNotNull($notificationA);
        $this->assertNotNull($notificationB);
        $legacyData = $notificationA->data;
        unset($legacyData['workspace_id']);
        $notificationA->update(['data' => $legacyData]);
        $this->assertArrayNotHasKey('workspace_id', $notificationA->fresh()->data);

        $this->inWorkspace($user, $workspaceB)->get(route('notifications.index'))
            ->assertSeeText('Workspace B notification')
            ->assertDontSeeText('Workspace A notification');
        $this->inWorkspace($user, $workspaceB)->patch(route('notifications.read', $notificationA->id))->assertNotFound();
        $this->assertNull($notificationA->fresh()->read_at);
        $this->inWorkspace($user, $workspaceB)->patch(route('notifications.read-all'))->assertRedirect();
        $this->assertNull($notificationA->fresh()->read_at);
        $this->assertNotNull($notificationB->fresh()->read_at);
        $this->assertSame($workspaceB->id, $notificationB->data['workspace_id']);
    }

    public function test_dashboard_team_count_uses_current_workspace_memberships(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $otherOwner = User::factory()->create();
        $workspace = $this->workspaceFor($owner, 'Current');
        $otherWorkspace = $this->workspaceFor($otherOwner, 'Other');
        WorkspaceMember::create(['workspace_id' => $workspace->id, 'user_id' => $member->id, 'role' => 'member', 'status' => 'active']);
        TeamMember::forceCreate(['workspace_id' => $otherWorkspace->id, 'user_id' => $otherOwner->id, 'name' => 'Legacy member']);

        $response = $this->inWorkspace($owner, $workspace)->get(route('dashboard'));

        $response->assertOk();
        $this->assertSame(2, $response->viewData('stats')['team']);
    }
}
