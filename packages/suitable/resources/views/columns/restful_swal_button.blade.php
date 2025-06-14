@if ($actions->isNotEmpty())
    @if ($actions->count() > 1)
        <div class="x-restful-buttons ui buttons" style="gap: 4px">
    @endif

    @if ($additionalButton)
        <x-volt-link-button url="{{ $actions->get('show') }}" up-layer="new" up-mode="modal" icon="radiation"
            class="icon secondary" style="background: #05C47E;border-radius: 4px;color: #FFFFFF;padding: 5px"
            @if ($additionalButton['target']) target="{{ $additionalButton['target'] }}" @endif />
    @endif

    @if ($actions->has('show'))
        <x-volt-link-button url="{{ $actions->get('show') }}" up-layer="new" up-mode="modal" icon="eye"
            class="icon secondary" style="background: #05C47E;border-radius: 4px;color: #FFFFFF;padding: 5px" />
    @endif

    @if ($actions->has('edit'))
        <x-volt-link-button url="{{ $actions->get('edit') }}" up-layer="new" up-mode="modal" icon="pencil"
            class="icon secondary" style="background: #3267E3;border-radius: 4px;color: #FFFFFF;padding: 5px" />
    @endif

    @if ($actions->has('destroy'))
        <form id="delete-form-{{ $data['id'] }}" action="{{ $actions->get('destroy') }}" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            {{ csrf_field() }}

            <x-volt-button icon="trash" class="icon secondary delete-btn-action" type="button"
                style="background: #CB3A31;border-radius: 4px;color: #FFFFFF;padding: 5px" data-id="{{ $data['id'] }}"
                data-confirmation="{{ $deleteConfirmation }}" />
        </form>
    @endif

    @if ($actions->count() > 1)
        </div>
    @endif
@endif

@push('script')
    @once
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .custom-swal-popup {
                width: 440px !important;
                padding: 16px !important;
                background: white !important;
                border-radius: 8px !important;
                font-family: 'Inter Tight', sans-serif !important;
            }

            .custom-swal-close {
                width: 20px !important;
                height: 20px !important;
                background: #F6F6F9 !important;
                border-radius: 24px !important;
                color: black !important;
                font-size: 12px !important;
                line-height: 1 !important;
                top: 16px !important;
                right: 16px !important;
                border: none !important;
                outline: 1px black solid !important;
                outline-offset: -0.50px !important;
            }

            .custom-swal-header {
                padding: 0 !important;
                border: none !important;
            }

            .custom-swal-icon {
                width: 48px !important;
                height: 48px !important;
                background: #FFF1F3 !important;
                border-radius: 6px !important;
                border: none !important;
                margin: 16px auto 8px auto !important;
                position: relative !important;
            }

            .custom-swal-icon .swal2-icon-content {
                display: none !important;
            }

            .custom-swal-icon::before {
                content: '' !important;
                width: 24px !important;
                height: 24px !important;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M3 6H21M19 6V20C19 21 18 22 17 22H7C6 22 5 21 5 20V6M8 6V4C8 3 9 2 10 2H14C15 2 16 3 16 4V6M10 11V17M14 11V17' stroke='%23DC1A32' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");                background-repeat: no-repeat !important;
                background-position: center !important;
                background-size: contain !important;
                font-size: 24px !important;
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
            }

            .custom-swal-title {
                color: #0D0D12 !important;
                font-size: 18px !important;
                font-family: 'Inter Tight', sans-serif !important;
                font-weight: 600 !important;
                line-height: 25.2px !important;
                letter-spacing: 0.2px !important;
                text-align: center !important;
                margin: 0 0 8px 0 !important;
                padding: 0 !important;
            }

            .custom-swal-content {
                color: #8588AB !important;
                font-size: 14px !important;
                font-family: 'Inter Tight', sans-serif !important;
                font-weight: 400 !important;
                line-height: 21px !important;
                letter-spacing: 0.2px !important;
                text-align: center !important;
                margin: 0 0 16px 0 !important;
                padding: 0 !important;
            }

            .custom-swal-actions {
                margin: 0 !important;
                padding: 0 !important;
                display: flex !important;
                gap: 8px !important;
                width: 100% !important;
            }

            .custom-swal-cancel {
                flex: 1 !important;
                padding: 10px 8px !important;
                border-radius: 8px !important;
                border: 1px solid #D5D6E2 !important;
                background: transparent !important;
                color: #515478 !important;
                font-size: 14px !important;
                font-family: 'Inter', sans-serif !important;
                font-weight: 500 !important;
                line-height: 20px !important;
                text-align: center !important;
                margin: 0 !important;
                cursor: pointer !important;
            }

            .custom-swal-confirm {
                flex: 1 !important;
                padding: 10px 8px !important;
                border-radius: 8px !important;
                border: none !important;
                background: #DC1A32 !important;
                color: white !important;
                font-size: 14px !important;
                font-family: 'Inter', sans-serif !important;
                font-weight: 500 !important;
                line-height: 20px !important;
                text-align: center !important;
                margin: 0 !important;
                cursor: pointer !important;
            }

            .custom-swal-confirm:hover {
                background: #B81429 !important;
            }

            .custom-swal-cancel:hover {
                background: #F6F6F9 !important;
            }
        </style>
    @endonce
@endpush

@if ($actions->has('destroy'))
    @once
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initSweetAlertDeleteHandlers();
            });

            function initSweetAlertDeleteHandlers() {
                document.addEventListener('click', function(event) {
                    const deleteBtn = event.target.closest('.delete-btn-action');

                    if (!deleteBtn) return;

                    const dataId = deleteBtn.getAttribute('data-id');
                    const confirmationText = deleteBtn.getAttribute('data-confirmation');
                    const form = document.getElementById('delete-form-' + dataId);

                    event.preventDefault();

                    if (!form) return;

                    Swal.fire({
                        title: 'Hapus',
                        text: 'Anda yakin ingin menghapus data ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus Data',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'custom-swal-popup',
                            closeButton: 'custom-swal-close',
                            header: 'custom-swal-header',
                            icon: 'custom-swal-icon',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-content',
                            actions: 'custom-swal-actions',
                            cancelButton: 'custom-swal-cancel',
                            confirmButton: 'custom-swal-confirm'
                        },
                        showCloseButton: true,
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Loading...',
                                html: 'Please wait while we delete the data',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();

                                    setTimeout(() => {
                                        const newForm = document.createElement('form');
                                        newForm.style.display = 'none';
                                        newForm.method = 'POST';
                                        newForm.action = form.action;

                                        const inputs = form.querySelectorAll('input');
                                        inputs.forEach(input => {
                                            const clonedInput = input.cloneNode(
                                                true);
                                            newForm.appendChild(clonedInput);
                                        });

                                        document.body.appendChild(newForm);
                                        newForm.submit();
                                    }, 100);
                                }
                            });
                        }
                    });
                });
            }
        </script>
    @endonce
@endif
