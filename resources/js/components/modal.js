window.LivewireModal = () => {
    return {
        show: false,
        loading: false,
        container: '.volt-modal-dimmer',
        activeModal: null,
        modalStack: [],
        init() {
            this.$watch('show', value => {
                if (value) {

                } else {
                    this.activeModal = null;
                }
            });

            Livewire.on('openModal', (modal) => {
                this.show = true;
                this.loading = true;
                this.activeModal = modal;
            });

            Livewire.on('closeModal', (count) => {
                this.close(count);
            });

            Livewire.hook('message.failed', (message, component) => {
                this.loading = false;
                this.activeModal = this.modalStack.at(-1);
                this.show = this.activeModal !== undefined;
            });

            Livewire.on('activeModalChanged', (modal) => {
                this.activeModal = modal;
                this.modalStack.push(modal);
                this.loading = false;
                setTimeout(() => {
                    this.$refs[modal].classList.remove('transition', 'scale', 'in');
                }, 300);
            });
        },
        close(count = 1) {
            let closedModal = [];
            for (let i = 0; i < count; i++) {
                const modal = this.modalStack.pop();
                Livewire.emit('modalClosed', modal);
                closedModal.push(modal);
            }

            closedModal.forEach((modal, index) => {
                let immediate = index > 0;

                if (this.$refs[modal] === undefined) {
                    return false;
                }

                if (this.modalStack.length === 0) {
                    if (immediate) {
                        this.show = false;
                        return;
                    }

                    this.$refs[modal].classList.add('transition', 'scale', 'out');
                    setTimeout(() => {
                        this.$refs[modal].classList.remove('transition', 'scale', 'out');
                        this.show = false;
                    }, 300);
                } else {

                    if (immediate) {
                        return;
                    }
                    this.$refs[modal].classList.add('transition', 'scale', 'out');

                    setTimeout(() => {
                        this.$refs[modal].classList.remove('transition', 'scale', 'out');
                    }, 300);
                }
            });

            if (this.modalStack.length > 0) {
                this.activeModal = this.modalStack.at(-1);
                this.$refs[this.activeModal].classList.add('transition', 'scale', 'in');

                setTimeout(() => {
                    this.$refs[this.activeModal].classList.remove('transition', 'scale', 'in');
                }, 300);
            }
        }
    };
}
