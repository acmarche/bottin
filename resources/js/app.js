import './bootstrap';

window.scrollToTab = function (tabName) {
    const tabs = document.querySelectorAll('[role="tab"]');
    for (const tab of tabs) {
        if (tab.textContent.toLowerCase().includes(tabName)) {
            tab.click();
            setTimeout(() => {
                tab.closest('[role="tablist"]')?.parentElement?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }, 100);
            break;
        }
    }
};
