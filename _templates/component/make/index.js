const { sites, prompts } = require('../../utils')

module.exports = {
  prompt: ({
    prompter,
    args: { site: givenSite, name: givenPath, props: hasProps },
    h,
  }) => {
    const regex = /^\/?sites\/(?<site>.+)\/src\/(?<path>.+)/
    const groups = givenPath?.match(regex)?.groups
    const { site, path } =
      !givenSite && groups?.site
        ? { site: groups?.site, path: groups?.path }
        : { site: givenSite, path: givenPath }

    return prompter
      .prompt([
        {
          ...prompts.site,
          name: 'site',
          initial: site,
          skip: sites.includes(site),
        },
        {
          ...prompts.path,
          name: 'path',
          hint: '例: components/Button',
          initial: path,
          skip: !!path,
          message:
            'ファイルを出力するパスを入力してください (src/ からの相対パス)',
        },
        {
          type: 'multiselect',
          name: 'files',
          message: '作成するファイルを選択してください',
          hint: '<PgUp>: 上へ | <PgDn>: 下へ | <Space>: 選択 ON/OFF | <Enter>: 決定',
          initial: ['component', 'stories'],
          choices: [
            {
              name: 'component',
              value: 'component',
              hint: 'コンポーネント (index.tsx)',
            },
            {
              name: 'stories',
              value: 'stories',
              hint: 'Storybook (index.stories.tsx)',
            },
            {
              name: 'jest',
              value: 'jest',
              hint: 'Jest テストケース (index.test.tsx)',
            },
          ],
        },
        {
          type: 'confirm',
          name: 'hasProps',
          message: 'Props を受け取るコンポーネントを作成しますか？',
          initial: hasProps,
          skip: typeof hasProps === 'boolean',
        },
      ])
      .then(({ site, path, files, hasProps }) => {
        const componentName = h.path.basename(path)
        const componentPath = `sites/${site}/src/${h.path.dirname(
          path
        )}/${componentName}`

        return {
          name: componentName,
          path: componentPath,
          hasProps,
          withComponent: files.includes('component'),
          withStories: files.includes('stories'),
          withJest: files.includes('jest'),
        }
      })
  },
}
