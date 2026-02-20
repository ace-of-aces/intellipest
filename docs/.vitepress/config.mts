import { defineConfig } from 'vitepress'
import pkg from '../package.json'
import llmstxt, { copyOrDownloadAsMarkdownButtons } from 'vitepress-plugin-llms'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "IntelliPest",
    description: "IntelliPest - Enhanced coding assistance for PestPHP.",
    srcDir: './pages',
    cleanUrls: true,
    markdown: {
        theme: {
            light: 'github-light',
            dark: 'github-dark'
        },
        config(md) {
            md.use(copyOrDownloadAsMarkdownButtons)
        }
    },
    vite: {
        plugins: [llmstxt()]
    },
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [
            { text: 'Home', link: '/' },
            {
                text: pkg.version,
                items: [
                    {
                        text: 'Changelog',
                        link: 'https://github.com/ace-of-aces/intellipest/blob/main/CHANGELOG.md'
                    },
                    {
                        text: 'Contributing',
                        link: 'https://github.com/ace-of-aces/intellipest/blob/main/CONTRIBUTING.md'
                    }
                ]
            }
        ],

        sidebar: [
            {
                text: 'Getting Started',
                items: [
                    { text: 'Quick Start', link: '/quick-start' },
                    { text: 'Recipes', link: '/recipes' },
                    { text: 'Configuration', link: '/configuration' },
                    { text: 'Compatibility', link: '/compatibility' },
                ]
            },
            {
                text: 'Advanced',
                items: [
                    { text: 'Known Limitations', link: '/limitations' },
                    { text: 'Under The Hood', link: '/under-the-hood' },
                ]
            },
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/ace-of-aces/intellipest' },
            { icon: 'x', link: 'https://x.com/julian_center' },
            {
                icon: {
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from Lucide by Lucide Contributors - https://github.com/lucide-icons/lucide/blob/main/LICENSE --><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20a14.5 14.5 0 0 0 0-20M2 12h20"/></g></svg>'
                },
                link: 'https://julian.center'
            }
        ],

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2026-present <a href="https://julian.center">Julian Schramm</a>'
        },

        search: {
            provider: 'local',
        }
    }
})
