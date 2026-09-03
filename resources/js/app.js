import "./bootstrap";
import Alpine from "alpinejs";
import Sortable from "sortablejs";

if (!document.documentElement.dataset.theme) {
    document.documentElement.dataset.theme =
        localStorage.getItem("flowbase-theme") ||
        (window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light");
}

window.Sortable = Sortable;
Alpine.data("themeToggle", () => ({
    dark: document.documentElement.dataset.theme === "dark",
    toggle() {
        this.dark = !this.dark;
        document.documentElement.dataset.theme = this.dark ? "dark" : "light";
        localStorage.setItem("flowbase-theme", this.dark ? "dark" : "light");
        window.dispatchEvent(
            new CustomEvent("flowbase-theme-changed", {
                detail: this.dark ? "dark" : "light",
            }),
        );
    },
}));
Alpine.data("clientAutocomplete", (clients, initial) => ({
    clients,
    selected: initial,
    query: initial?.name || "",
    open: false,
    active: 0,
    get results() {
        const q = this.query.toLowerCase().trim();
        return q
            ? this.clients
                  .filter((client) =>
                      `${client.name} ${client.email || ""}`
                          .toLowerCase()
                          .includes(q),
                  )
                  .slice(0, 8)
            : this.clients.slice(0, 8);
    },
    choose(client) {
        if (!client) return;
        this.selected = client;
        this.query = client.name;
        this.open = false;
    },
    clear() {
        this.selected = null;
        this.query = "";
        this.open = true;
        this.active = 0;
        this.$nextTick(() => this.$refs.input.focus());
    },
    move(step) {
        if (!this.open) this.open = true;
        if (!this.results.length) return;
        this.active =
            (this.active + step + this.results.length) % this.results.length;
    },
}));
window.Alpine = Alpine;
Alpine.start();
document.addEventListener("DOMContentLoaded", () => {
    const reduced = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
        ).matches,
        shell = document.getElementById("appShell"),
        sidebar = document.getElementById("sidebar"),
        overlay = document.getElementById("sidebarOverlay");
    const openMenu = () => {
            sidebar?.classList.remove("-translate-x-full");
            overlay?.classList.remove("hidden");
            document.getElementById("menuButton")?.setAttribute("aria-expanded", "true");
            document.getElementById("menuButtonBottom")?.setAttribute("aria-expanded", "true");
            sidebar?.querySelector("a, button, select")?.focus();
        },
        closeMenu = () => {
            sidebar?.classList.add("-translate-x-full");
            overlay?.classList.add("hidden");
            document.getElementById("menuButton")?.setAttribute("aria-expanded", "false");
            document.getElementById("menuButtonBottom")?.setAttribute("aria-expanded", "false");
        };
    document.getElementById("menuButton")?.addEventListener("click", openMenu);
    overlay?.addEventListener("click", closeMenu);
    document
        .getElementById("menuButtonBottom")
        ?.addEventListener("click", openMenu);
    sidebar?.querySelectorAll("a").forEach((link) =>
        link.addEventListener("click", () => {
            if (window.matchMedia("(max-width: 1023px)").matches) closeMenu();
        }),
    );
    const collapse = document.getElementById("collapseSidebar");
    if (localStorage.getItem("flowbase-sidebar") === "collapsed")
        shell?.classList.add("is-collapsed");
    collapse?.addEventListener("click", () => {
        shell?.classList.toggle("is-collapsed");
        localStorage.setItem(
            "flowbase-sidebar",
            shell?.classList.contains("is-collapsed")
                ? "collapsed"
                : "expanded",
        );
    });
    const toast = (message, type = "success") => {
        const region = document.getElementById("toastRegion");
        if (!region || !message) return;
        const palette =
            {
                success: [
                    "border-emerald-200",
                    "bg-emerald-50",
                    "text-emerald-600",
                    "✓",
                ],
                error: ["border-rose-200", "bg-rose-50", "text-rose-600", "!"],
                warning: [
                    "border-amber-200",
                    "bg-amber-50",
                    "text-amber-600",
                    "!",
                ],
                info: [
                    "border-indigo-200",
                    "bg-indigo-50",
                    "text-indigo-600",
                    "i",
                ],
            }[type] || [];
        const el = document.createElement("div");
        el.className = `toast ${palette[0]}`;
        el.innerHTML = `<span class="grid h-6 w-6 shrink-0 place-items-center rounded-full ${palette[1]} ${palette[2]} text-xs font-bold">${palette[3]}</span><p class="flex-1 pt-0.5 text-sm font-semibold text-slate-700"></p><button class="text-slate-400 transition hover:text-slate-700" aria-label="Dismiss">×</button>`;
        el.querySelector("p").textContent = message;
        const remove = () => {
            el.classList.add("is-leaving");
            setTimeout(() => el.remove(), 260);
        };
        el.querySelector("button").addEventListener("click", remove);
        region.append(el);
        setTimeout(remove, 4800);
    };
    window.Flowbase = { toast };
    document
        .querySelectorAll("[data-flash]")
        .forEach((el) => toast(el.dataset.message, el.dataset.type));
    let pendingForm;
    const modal = document.getElementById("confirmModal"),
        closeModal = () => modal?.classList.replace("flex", "hidden"),
        trapModalFocus = (event) => {
            if (event.key !== "Tab" || !modal?.classList.contains("flex")) return;
            const focusable = [...modal.querySelectorAll(
                'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
            )].filter((element) => !element.hidden);
            if (!focusable.length) return;
            const first = focusable[0], last = focusable.at(-1);
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        };
    document.querySelectorAll("form[data-confirm]").forEach((form) =>
        form.addEventListener("submit", (e) => {
            if (form.dataset.confirmed) return;
            e.preventDefault();
            pendingForm = form;
            document.getElementById("confirmMessage").textContent =
                form.dataset.confirm;
            modal?.classList.replace("hidden", "flex");
            document.getElementById("cancelConfirm")?.focus();
        }),
    );
    document
        .getElementById("cancelConfirm")
        ?.addEventListener("click", closeModal);
    modal
        ?.querySelector("[data-modal-backdrop]")
        ?.addEventListener("click", closeModal);
    document.getElementById("confirmSubmit")?.addEventListener("click", (e) => {
        if (!pendingForm) return;
        e.currentTarget.disabled = true;
        e.currentTarget.textContent = "Deleting…";
        pendingForm.dataset.confirmed = "true";
        pendingForm.submit();
    });
    document.addEventListener("keydown", (e) => {
        trapModalFocus(e);
        if (e.key === "Escape") {
            closeModal();
            closeMenu();
        }
    });
    document.querySelectorAll("form").forEach((form) =>
        form.addEventListener("submit", () => {
            if (form.dataset.confirm && !form.dataset.confirmed) return;
            const button = form.querySelector('button[type="submit"]');
            if (button && !button.dataset.submitting) {
                button.dataset.submitting = "true";
                button.disabled = true;
                button.textContent = button.dataset.loading || "Saving…";
            }
        }),
    );
    if (!reduced)
        requestAnimationFrame(() =>
            document
                .querySelectorAll(".progress-bar[data-progress]")
                .forEach(
                    (bar) => (bar.style.width = `${bar.dataset.progress}%`),
                ),
        );
    let installEvent;
    window.addEventListener("beforeinstallprompt", (event) => {
        event.preventDefault();
        installEvent = event;
        if (localStorage.getItem("flowbase-install-later") !== "1") {
            const banner = document.createElement("div");
            banner.className = "install-banner";
            banner.innerHTML =
                '<b>📱 Install Project Management</b><span>Get a faster app experience.</span><button>Install</button><button aria-label="Dismiss">Later</button>';
            banner.querySelector("button").onclick = async () => {
                await installEvent.prompt();
                banner.remove();
            };
            banner.querySelectorAll("button")[1].onclick = () => {
                localStorage.setItem("flowbase-install-later", "1");
                banner.remove();
            };
            document.body.append(banner);
        }
    });
    if ("serviceWorker" in navigator)
        window.addEventListener("load", () =>
            navigator.serviceWorker.register("/sw.js"),
        );
});
