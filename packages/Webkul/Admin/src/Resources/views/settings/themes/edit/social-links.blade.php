<v-social-links :errors="errors"></v-social-links>

<!-- Social Links Vue Component -->
@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-social-links-template"
    >
        <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                <div class="mb-2.5 flex items-center justify-between gap-x-2.5">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            Social Links
                        </p>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                            Add social media links with custom icons for the footer.
                        </p>
                    </div>

                    <div
                        class="secondary-button"
                        @click="isUpdating=false;$refs.addSocialLinkModal.toggle()"
                    >
                        Add Social Link
                    </div>
                </div>

                <!-- List of Social Links -->
                <div v-if="socialLinks.length">
                    <div
                        class="grid border-b border-slate-300 last:border-b-0 dark:border-gray-800 py-5"
                        v-for="(link, index) in socialLinks"
                        :key="index"
                    >
                        <input type="hidden" :name="'{{ is_string($currentLocale) ? $currentLocale : $currentLocale->code }}[options][' + index + '][title]'" :value="link.title" />
                        <input type="hidden" :name="'{{ is_string($currentLocale) ? $currentLocale : $currentLocale->code }}[options][' + index + '][url]'" :value="link.url" />
                        <input type="hidden" :name="'{{ is_string($currentLocale) ? $currentLocale : $currentLocale->code }}[options][' + index + '][icon_svg]'" :value="link.icon_svg" />
                        <input type="hidden" :name="'{{ is_string($currentLocale) ? $currentLocale : $currentLocale->code }}[options][' + index + '][sort_order]'" :value="link.sort_order" />

                        <div class="flex justify-between items-center">
                            <div class="flex gap-4 items-center">
                                <div class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded text-gray-500" v-html="link.icon_svg"></div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">@{{ link.title }}</p>
                                    <p class="text-sm text-blue-600 truncate max-w-xs">@{{ link.url }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <p class="cursor-pointer text-blue-600 hover:underline" @click="edit(link, index)">Edit</p>
                                <p class="cursor-pointer text-red-600 hover:underline" @click="remove(index)">Delete</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="py-10 text-center text-gray-400">
                    No social links added yet.
                </div>
            </div>

            <!-- Modal for Adding/Editing Social Links -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="socialLinkModalForm"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    <x-admin::modal ref="addSocialLinkModal">
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @{{ isUpdating ? 'Edit Social Link' : 'Add Social Link' }}
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <v-field type="hidden" name="index" />

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Title</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="title" rules="required" placeholder="e.g. Telegram" />
                                <x-admin::form.control-group.error control-name="title" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">URL</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="url" rules="required|url" placeholder="https://t.me/yourchannel" />
                                <x-admin::form.control-group.error control-name="url" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Icon SVG (Path content or full SVG)</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="textarea" name="icon_svg" rules="required" placeholder="<svg>...</svg> or <path d='...'/>" />
                                <x-admin::form.control-group.error control-name="icon_svg" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Sort Order</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="sort_order" rules="required|numeric" value="0" />
                                <x-admin::form.control-group.error control-name="sort_order" />
                            </x-admin::form.control-group>
                        </x-slot>

                        <x-slot:footer>
                            <button type="submit" class="primary-button">Save</button>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-social-links', {
            template: '#v-social-links-template',
            props: ['errors'],
            data() {
                return {
                    socialLinks: @json($theme->translate(is_string($currentLocale) ? $currentLocale : $currentLocale->code)['options'] ?? []),
                    isUpdating: false,
                };
            },
            methods: {
                updateOrCreate(params) {
                    if (this.isUpdating) {
                        this.socialLinks[params.index] = { ...params };
                    } else {
                        this.socialLinks.push({ ...params });
                    }
                    this.$refs.addSocialLinkModal.toggle();
                },
                edit(link, index) {
                    this.isUpdating = true;
                    this.$refs.socialLinkModalForm.setValues({ ...link, index });
                    this.$refs.addSocialLinkModal.toggle();
                },
                remove(index) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.socialLinks.splice(index, 1);
                        }
                    });
                }
            }
        });
    </script>
@endPushOnce
