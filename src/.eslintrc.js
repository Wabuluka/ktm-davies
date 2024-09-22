module.exports = {
    root: true,
    "env": {
        "browser": true,
        "es2021": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:react/recommended",
        "plugin:react-hooks/recommended",
        "plugin:@typescript-eslint/recommended",
        "prettier"
    ],
    "overrides": [
    ],
    "parser": "@typescript-eslint/parser",
    "parserOptions": {
        "ecmaVersion": "latest",
        "sourceType": "module"
    },
    "plugins": [
        "react",
        "@typescript-eslint"
    ],
    "ignorePatterns": ["/*.js", "public/build/**/*.js", "vendor/**/*.js"],
    "rules": {
        "react/react-in-jsx-scope": "off",
        "react/jsx-no-useless-fragment": "error",
        'no-restricted-imports': [
            'error',
            {
                patterns: ['@/Features/*/*'],
                patterns: ['@/Layouts/*/*'],
            },
        ],
        "@typescript-eslint/no-unused-vars": [
            "error",
            {
                "argsIgnorePattern": "^_",
                "varsIgnorePattern": "^_",
                "caughtErrorsIgnorePattern": "^_",
                "destructuredArrayIgnorePattern": "^_"
            },
        ],
    },
    "settings": {
        "react": {
          "version": "detect"
        }
    },
}
