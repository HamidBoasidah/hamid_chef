module.exports = {
	darkMode: 'class',
	content: [
		'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
		'./storage/framework/views/*.php',
		'./resources/**/*.blade.php',
		'./resources/**/*.js',
		'./resources/**/*.vue',
	],
	theme: {
		extend: {
			fontFamily: {
				sans: ['Figtree', ...require('tailwindcss/defaultTheme').fontFamily.sans],
				arabic: ['Air Strip Arabic', 'Arial', 'sans-serif'],
				english: ['Ahlan', 'Arial', 'sans-serif'],
			},
			colors: {
				primary: {
					DEFAULT: '#083064',
					50: '#E6EBF2',
					100: '#CCD7E5',
					200: '#99AFCB',
					300: '#6687B1',
					400: '#335F97',
					500: '#083064',
					600: '#062650',
					700: '#051D3C',
					800: '#031328',
					900: '#020A14',
				},
				secondary: {
					DEFAULT: '#CBE4F8',
					50: '#FFFFFF',
					100: '#FFFFFF',
					200: '#FFFFFF',
					300: '#F5FAFD',
					400: '#E0EFF9',
					500: '#CBE4F8',
					600: '#A3D1F3',
					700: '#7BBEEE',
					800: '#53ABE9',
					900: '#2B98E4',
				},
			}
		}
	},
	plugins: [
		require('tailwindcss-rtl'),
		require('@tailwindcss/forms'),
	],
}
