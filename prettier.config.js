module.exports = {
  singleQuote: true,
  semi: false,
  trailingComma: 'es5',
  plugins: ['prettier-plugin-tailwindcss'],
  overrides: [
    {
      files: 'sites/blackcherry/**',
      options: {
        tailwindConfig: './sites/blackcherry/tailwind.config.js',
      },
    },
    {
      files: 'sites/ceriserose/**',
      options: {
        tailwindConfig: './sites/ceriserose/tailwind.config.js',
      },
    },
    {
      files: 'sites/chocolatsucre/**',
      options: {
        tailwindConfig: './sites/chocolatsucre/tailwind.config.js',
      },
    },
    {
      files: 'sites/ktcom/**',
      options: {
        tailwindConfig: './sites/ktcom/tailwind.config.js',
      },
    },
  ],
}
