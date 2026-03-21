<v-date-picker {{ $attributes }}>
    {{ $slot }}
</v-date-picker>

@pushOnce('scripts')
    <script type="text/x-template" id="v-date-picker-template">
                <span class="relative block w-full">
                    <slot></slot>
                </span>
            </script>

    <script type="module">
        app.component('v-date-picker', {
            template: '#v-date-picker-template',

            props: {
                name: String,

                value: String,

                allowInput: {
                    type: Boolean,
                    default: true,
                },

                disable: Array,

                minDate: String,

                maxDate: String,
            },

            data: function () {
                return {
                    datepicker: null
                };
            },

            mounted: function () {
                let options = this.setOptions();

                this.activate(options);
            },

            methods: {
                setOptions: function () {
                    let self = this;

                    return {
                        allowInput: this.allowInput ?? true,
                        clickOpens: true,
                        disable: this.disable ?? [],
                        minDate: this.minDate ?? '',
                        maxDate: this.maxDate ?? '',
                        altFormat: "Y-m-d",
                        dateFormat: "Y-m-d",
                        weekNumbers: true,

                        onChange: function (selectedDates, dateStr, instance) {
                            self.$emit("onChange", dateStr);
                        },

                        onOpen: function (selectedDates, dateStr, instance) {
                            if (window.innerWidth < 768) {
                                instance.calendarContainer.classList.add('mobile-center');
                            }
                        },

                        onClose: function (selectedDates, dateStr, instance) {
                            instance.calendarContainer.classList.remove('mobile-center');
                        }
                    };
                },

                activate: function (options) {
                    let self = this;
                    let element = this.$el.getElementsByTagName("input")[0];

                    this.datepicker = new Flatpickr(element, options);

                    // Force open on click for better reliability
                    element.addEventListener('click', function () {
                        self.datepicker.open();
                    });
                },

                clear: function () {
                    this.datepicker.clear();
                }
            }
        });
    </script>

    <style>
        @media (max-width: 767px) {
            .flatpickr-calendar.mobile-center {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                margin: 0 !important;
                z-index: 999999 !important;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3) !important;
                animation: flatpickrFadeIn 0.2s ease-out;
            }

            .flatpickr-calendar.mobile-center.animate.open {
                animation: fpFadeInCenter 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            }

            @keyframes fpFadeInCenter {
                from {
                    opacity: 0;
                    transform: translate(-50%, -45%);
                }
                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }
        }
    </style>
@endPushOnce