<template>
    <default-field :field="field">
        <template slot="field">
            <a
                    class="inline-block font-bold cursor-pointer mr-2 animate-text-color select-none"
                    :class="{ 'text-60': localeKey !== currentLocale, 'text-primary border-b-2': localeKey == currentLocale }"
                    :key="`a-${localeKey}`"
                    v-for="(locale, localeKey) in field.locales"
                    @click="changeTab(localeKey)"
            >
                {{ locale }}
            </a>

            <textarea
                    ref="field"
                    :id="field.name"
                    class="mt-4 w-full form-control form-input form-input-bordered py-3 min-h-textarea"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="value[currentLocaleCode]"
                    v-if="!field.singleLine && !field.trix"
                    @keydown.tab="handleTab"
            ></textarea>

            <div v-if="!field.singleField && field.trix" class="mt-4">
                <trix
                        ref="field"
                        name="trixman"
                        :value="value[currentLocaleCode]"
                        placeholder=""
                        @change="handleChange"

                />
            </div>

            <input
                    ref="field"
                    type="text"
                    :id="field.name"
                    class="mt-4 w-full form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="value[currentLocaleCode]"
                    v-if="field.singleLine"
                    @keydown.tab="handleTab"
            />

            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<script>

    import Trix from '../Trix'

    import {FormField, HandlesValidationErrors} from 'laravel-nova'
    import {EventBus} from '../event-bus';

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        components: {Trix},

        data() {
            return {
                locales: Object.keys(this.field.locales),
                currentLocale: null, // an index
                currentLocaleCode: null, // an iso 8859-1 code
            }
        },

        mounted() {
            this.changeTab(this.locales[0] || null, true);
            EventBus.$on('localeChanged', locale => {
                if (this.currentLocale !== locale) {
                    this.changeTab(locale, true);
                }
            });
        },

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || {}
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                Object.keys(this.value).forEach(locale => {
                    formData.append(this.field.attribute + '[' + locale + ']', this.value[locale] || '')
                })
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value[this.currentLocaleCode] = value
            },

            changeTab(locale, dontEmit) {
                if (this.currentLocale !== locale) {
                    if (!dontEmit) {
                        EventBus.$emit('localeChanged', locale);
                    }
                    this.changeLocale(locale);

                    this.$nextTick(() => {
                        if (this.field.trix) {
                            this.$refs.field.update()
                        } else {
                            this.$refs.field.focus()
                        }
                    })
                }
            },
            changeLocale(locale) {
                this.currentLocale = locale;
                this.currentLocaleCode = this.field.locales[locale];
            },
            handleTab(e) {
                const currentIndex = this.locales.indexOf(this.currentLocale);
                if (!e.shiftKey) {
                    if (currentIndex < this.locales.length - 1) {
                        e.preventDefault();
                        this.changeTab(this.locales[currentIndex + 1]);
                    }
                } else {
                    if (currentIndex > 0) {
                        e.preventDefault();
                        this.changeTab(this.locales[currentIndex - 1]);
                    }
                }
            }
        }
    }
</script>
