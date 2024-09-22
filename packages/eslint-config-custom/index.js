module.exports = {
  extends: ['next', 'turbo', 'plugin:storybook/recommended', 'prettier'],
  plugins: ['import', 'unused-imports'],
  rules: {
    '@next/next/no-html-link-for-pages': 'off',
    'import/no-default-export': 'off',
    'import/order': 'error',
    'no-restricted-imports': [
      'error',
      {
        patterns: ['..*'],
      },
    ],
    'react/function-component-definition': [
      'error',
      {
        namedComponents: ['function-declaration', 'function-expression'],
        unnamedComponents: 'function-expression',
      },
    ],
    'react/jsx-no-useless-fragment': 'error',
    'unused-imports/no-unused-imports': 'error',
  },
  overrides: [
    {
      files: [
        './*/!(app|pages)/**/!(*.stories|*.test|*.spec).+(js|jsx|ts|tsx)',
        './*/app/**/_*/**/!(*.stories|*.test|*.spec).+(js|jsx|ts|tsx)',
      ],
      rules: {
        'import/no-default-export': 'error',
      },
    },
  ],
  ignorePatterns: ['src/generated/'],
}
