<template>
    <panel-item :field="field">
        <template slot="value">

            <a
                    class="inline-block font-bold cursor-pointer mr-2 animate-text-color select-none"
                    :class="{ 'text-60': localeKey !== currentLocale, 'text-primary border-b-2': localeKey == currentLocale }"
                    :key="`a-${localeKey}`"
                    v-for="(locale, localeKey) in field.locales"
                    @click="changeTab(localeKey)"
            >
                {{ locale }}
            </a>

            <div class="mt-4">
                <span v-if="field.value[currentLocaleCode]" v-html="field.value[currentLocaleCode]"></span>
                <span v-else>—</span>
            </div>

        </template>
    </panel-item>
</template>

<script>
    import {EventBus} from '../event-bus';

    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],

        data() {
            return {
                currentLocale: Object.keys(this.field.locales)[0],
                currentLocaleCode: Object.values(this.field.locales)[0]
            }
        },
        mounted() {
            EventBus.$on('localeChanged', locale => {
                if (this.currentLocale !== locale) {
                    this.changeTab(locale, true);
                }
            });
        },
        methods: {
            changeTab(locale, dontEmit) {
                if (this.currentLocale !== locale) {
                    if (!dontEmit) {
                        EventBus.$emit('localeChanged', locale);
                    }
                    this.changeLocale(locale);
                }
            },
            changeLocale(locale) {
                this.currentLocale = locale;
                this.currentLocaleCode = Object.values(this.field.locales)[locale];
            }
        }
    }
</script>
