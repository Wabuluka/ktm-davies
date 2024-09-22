import { ChonkyActions, ChonkyIconName, defineFileAction } from 'chonky';

const defaultUploadFiles = ChonkyActions.UploadFiles;
const defaultCreateFolder = ChonkyActions.CreateFolder;
const defaultDeleteFiles = ChonkyActions.DeleteFiles;

export const ChonkyCustomActions = {
  UploadFiles: defineFileAction({
    ...defaultUploadFiles,
    button: { ...defaultUploadFiles.button, name: 'Upload', group: undefined },
  } as const),

  CreateFolder: defineFileAction({
    ...defaultCreateFolder,
    button: { ...defaultCreateFolder.button, name: 'Create', group: undefined },
  } as const),

  RenameFiles: defineFileAction({
    // FIXME: 単一選択時のみアクティブになるオプションがライブラリ側で追加されたら対応 (https://github.com/TimboKZ/Chonky/issues/54)
    id: 'rename_files',
    requiresSelection: true,
    button: {
      name: 'Rename',
      toolbar: true,
      contextMenu: true,
      icon: ChonkyIconName.file,
    },
  } as const),

  DeleteFiles: defineFileAction({
    ...defaultDeleteFiles,
    button: { ...defaultDeleteFiles.button, name: 'Delete', group: undefined },
  } as const),
};
