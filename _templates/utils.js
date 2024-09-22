const fs = require('fs')
const dirs = fs.readdirSync('./sites', { withFileTypes: true })
const sites = dirs
  .filter((d) => d.isDirectory() && !d.name.startsWith('.'))
  .map((d) => d.name)

module.exports = {
  sites,
  prompts: {
    site: {
      type: 'select',
      name: 'site',
      message: 'サイトを選択してください',
      hint: '<PgUp>: 上へ | <PgDn>: 下へ | <Enter>: 決定',
      choices: sites,
    },
    path: {
      type: 'input',
      name: 'path',
      message: 'パスを入力してください (src/ からの相対パス)',
    },
  },
}
