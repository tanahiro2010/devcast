// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import sitemap from '@astrojs/sitemap';

// https://astro.build/config
export default defineConfig({
	site: 'https://devcast.work',
	integrations: [
		sitemap({
			filter: (page) => !page.endsWith('/404/'),
		}),
	],
	vite: {
		plugins: [tailwindcss()],
	},
});
