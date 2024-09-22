const path = require("path");

module.exports = {
  buildNextLintCommand: (dir) => {
    const appRoot = `${process.cwd()}${dir}`;

    return (filenames) =>
      `next lint --fix --file ${filenames
        .map((f) => path.relative(appRoot, f))
        .join(" --file ")}`;
  },
};
