document.addEventListener("DOMContentLoaded", function () {
  const navToggle = document.querySelector("[data-nav-toggle]");
  const navMenu = document.querySelector("[data-nav-menu]");

  if (!navToggle || !navMenu) return;

  function setMenu(open) {
    navToggle.setAttribute("aria-expanded", String(open));
    navMenu.setAttribute("data-visible", open ? "true" : "false");
    // manage inertness for better keyboard UX (simple approach)
    const links = navMenu.querySelectorAll("a");
    links.forEach((link) => {
      if (!open) {
        link.setAttribute("tabindex", "-1");
      } else {
        link.removeAttribute("tabindex");
      }
    });
  }

  // Initialize
  setMenu(navMenu.getAttribute("data-visible") === "true");

  navToggle.addEventListener("click", function (e) {
    const expanded = navToggle.getAttribute("aria-expanded") === "true";
    setMenu(!expanded);
    if (!expanded) {
      // focus first link
      const firstLink = navMenu.querySelector("a");
      if (firstLink) firstLink.focus();
    } else {
      navToggle.focus();
    }
  });

  // Close on Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      setMenu(false);
    }
  });

  // Close when clicking outside
  document.addEventListener("click", function (e) {
    const target = e.target;
    if (!navMenu.contains(target) && !navToggle.contains(target)) {
      setMenu(false);
    }
  });
});
