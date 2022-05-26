const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './templates/**/*.html.twig',
    './templates/**/**/*.html.twig',
    './templates/*.html.twig',
    './vendor/pixeldev/sulu-block-bundle/Resources/views/*.html.twig',
    './vendor/pixeldev/sulu-block-bundle/Resources/views/html5/*.html.twig'
  ],
  safelist : [
  ],
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      primary: '#ececec',
      secondary: '#1B1717',
      gris: '#F6F6F6',
      black: '#000000',
      white: '#FFFFFF',
    },
    fontFamily: {
      'sans': ['Inter', ...defaultTheme.fontFamily.sans ],
      'serif': ['Libre Baskerville', ...defaultTheme.fontFamily.serif],
    },
    extend: {
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ],
}
