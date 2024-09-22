import FileManager from '@/Features/FileManager/Components/FileManager';
import { ChonkyCustomActions } from '@/Features/FileManager/CustomActions';
import { useCreateFolderMutation } from '@/Features/FileManager/Hooks/useCreateFolderMutation';
import { useDeleteFileMutation } from '@/Features/FileManager/Hooks/useDeleteFileMutation';
import { useEditFileMutation } from '@/Features/FileManager/Hooks/useEditFileMutation';
import { InsertImagePayload } from '@/Features/RichTextEditor/plugins/ImagesPlugin';
import {
  ChonkyActions,
  ChonkyFileActionData,
  FileData,
  ChonkyActionUnion,
  GenericFileActionHandler,
} from 'chonky';
import { QueryKey, useQueryClient } from 'react-query';
import { useNativeFilePicker } from './useNativeFilePicker';
import { usePostFileMutation } from './usePostFileMutation';

type CustomActionUnion =
  (typeof ChonkyCustomActions)[keyof typeof ChonkyCustomActions];
type CustomFileActionHandler = GenericFileActionHandler<
  ChonkyActionUnion | CustomActionUnion
>;

// TEMP: ↓↓↓ バグ修正_サムネイルの誤設定を修正 ↓↓↓
const sortableActionIds: ChonkyFileActionData['id'][] = [
  ChonkyActions.SortFilesByName.id,
  ChonkyActions.SortFilesByDate.id,
  ChonkyActions.SortFilesBySize.id,
  ChonkyActions.ToggleShowFoldersFirst.id,
];
// TEMP: ↑↑↑ バグ修正_サムネイルの誤設定を修正 ↑↑↑

export const useFileActionHandler = (
  currentFolder: FileData,
  setcurrentFolder: React.Dispatch<React.SetStateAction<FileData>>,
  queryKey: QueryKey,
  insertFileIntoEditor?: (payload: InsertImagePayload) => void,
  mimeType?: React.ComponentProps<typeof FileManager>['mimeType'],
) => {
  const queryClient = useQueryClient();
  const postFileMutation = usePostFileMutation();
  const editFileMutation = useEditFileMutation();
  const createFolderMutation = useCreateFolderMutation();
  const deleteFileMutation = useDeleteFileMutation();

  const handleChangeInput: EventListener = (e) => {
    if (!(e.target instanceof HTMLInputElement)) return;

    const file = e.target.files?.[0];
    if (!file) return;

    postFileMutation.mutate(
      { file, folderId: currentFolder.id },
      {
        onSuccess: () => queryClient.invalidateQueries(queryKey),
      },
    );
    e.target.value = '';
  };

  const openNativeFilePicker = useNativeFilePicker({
    handleChangeInput,
    accept: mimeType === 'image/' ? 'image/*' : undefined,
  });

  const fileActionHandler: CustomFileActionHandler = (actionData) => {
    switch (actionData.id) {
      case ChonkyActions.OpenFiles.id: {
        const target = actionData.payload.targetFile;
        if (target?.isDir) {
          setcurrentFolder(target);
        } else if (insertFileIntoEditor) {
          insertFileIntoEditor({
            altText: '',
            src: target?.src ?? undefined,
          });
        }
        break;
      }
      case ChonkyActions.UploadFiles.id:
        openNativeFilePicker();
        break;
      case ChonkyActions.CreateFolder.id: {
        const newName = prompt(`新しいフォルダの名前`);
        const parentId = currentFolder.id;
        if (newName) {
          createFolderMutation.mutate(
            { name: newName, parentId: parentId },
            {
              onSuccess: () => queryClient.invalidateQueries(queryKey),
            },
          );
        }
        break;
      }
      case ChonkyCustomActions.RenameFiles.id: {
        const target = actionData.state.selectedFiles[0];
        const newName = prompt(`${target.name}を編集`);
        const id = target.id.match(/\d+/)?.toString();
        const isDir = target.isDir;
        if (newName && id)
          editFileMutation.mutate(
            { id: id, name: newName, isDir: isDir },
            {
              onSuccess: () => queryClient.invalidateQueries(queryKey),
            },
          );

        break;
      }
      case ChonkyActions.DeleteFiles.id: {
        const targets = actionData.state.selectedFiles;
        if (
          window.confirm(
            `選択された ${targets.length} 項目を削除しますか？\n他の書籍の説明欄や NEWS の本文で使用されている場合、それらも表示されなくなります。`,
          )
        ) {
          deleteFileMutation.mutate(
            { files: targets },
            {
              onSuccess: () => queryClient.invalidateQueries(queryKey),
            },
          );
        }
        break;
      }
      // TEMP: ↓↓↓ バグ修正_サムネイルの誤設定を修正 ↓↓↓
      /*
        バグ概要: ファイルの表示順を変更後にファイルビュー内をスクロールすると、本来サムネイル未設定のはずのファイルに別のファイルのサムネイルが設定される。(Chonky_v2.3.2)
        対応: ファイルの表示順が変更された場合にファイルリストを更新する。
        */
      case sortableActionIds.find((id) => id === actionData.id):
        queryClient.resetQueries(queryKey);
        break;
      // TEMP: ↑↑↑ バグ修正_サムネイルの誤設定を修正 ↑↑↑
    }
  };

  return fileActionHandler;
};
