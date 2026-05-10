// resources/js/components/toolbar/Toolbar.js

export const Toolbar = {
    get el() { return document.getElementById('main-toolbar'); },
    get left() { return document.getElementById('toolbar-left'); },
    get center() { return document.getElementById('toolbar-center'); },
    get right() { return document.getElementById('toolbar-right'); },

    setActions(config) {
        if (!this.el) return;

        this.clear();
        
        const updateSection = (el, content) => {
            if (el) {
                el.innerHTML = content || '';
                el.style.display = content ? 'flex' : 'none'; 
            }
        };

        updateSection(this.left, config.left);
        updateSection(this.center, config.center);
        updateSection(this.right, config.right);

        this.show();
    },

    show() {
        this.el.classList.add('is-active');
    },

    hide() {
        this.el.classList.remove('is-active');
    },

    clear() {
        if (this.left) this.left.innerHTML = '';
        if (this.center) this.center.innerHTML = '';
        if (this.right) this.right.innerHTML = '';
    }
};