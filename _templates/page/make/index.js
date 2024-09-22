const { sites, prompts } = require('../../utils')

module.exports = {
  prompt: ({
    prompter,
    args: { site: givenSite, name: givenPath, layout },
    h,
  }) => {
    const regex = /^\/?sites\/(?<site>.+)\/src\/app\/(?<path>.+)/
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
          hint: '例: books/[id]/edit',
          initial: path,
          skip: !!path,
          message:
            'page.tsx を出力するパスを入力してください (src/app/ からの相対パス)',
        },
        {
          type: 'confirm',
          name: 'withLayout',
          message: 'layout.tsx も作成しますか？',
          initial: !!layout,
          skip: typeof layout === 'boolean',
        },
      ])
      .then(({ site, path, withLayout }) => {
        const rootPath = `sites/${site}/src/app`
        const fixedPath = path.replace(/(^\/+|\/+$)/g, '')
        /** @type {string[]} */
        const segments = []
        /** @type {{ name: string, type: 'string' | 'string[]', optional: boolean}} */
        const params = []
        fixedPath.split('/').forEach((segment) => {
          const name = segment.replace(/[\.\[\]]/g, '')
          segments.push(name)
          if (segment.startsWith('[[')) {
            params.push({ name, type: 'string[]', optional: false })
          } else if (segment.startsWith('[...')) {
            params.push({ name, type: 'string[]', optional: true })
          } else if (segment.startsWith('[')) {
            params.push({ name, type: 'string', optional: false })
          }
        })
        const routeGroupRegex = /\(.*?\)/
        const componentName = h.inflection.camelize(
          segments
            .map((s) => s.replace('-', '_').replace(routeGroupRegex, ''))
            .join('_')
        )

        return {
          name: componentName,
          pagePath: `${rootPath}/${fixedPath}/page.tsx`,
          layoutPath: withLayout ? `${rootPath}/${fixedPath}/layout.tsx` : null,
          segments,
          params,
        }
      })
  },
}
