import { InsertImagePayload } from '@/Features/RichTextEditor/plugins/ImagesPlugin';
import {
  ChonkyActions,
  FileData,
  FullFileBrowser,
  setChonkyDefaults,
} from 'chonky';
import { ChonkyIconFA } from 'chonky-icon-fontawesome';
import { FC, useState } from 'react';
import { ChonkyCustomActions } from '../CustomActions';
import { useFetchFilesQuery } from '../Hooks/useFetchFilesQuery';
import { useFileActionHandler } from '../Hooks/useFileActionHandler';
import { useFolderChain } from '../Hooks/useFolderChian';

type Props = {
  width?: string;
  height?: string;
  insertFileIntoEditor?: (payload: InsertImagePayload) => void;
  mimeType?: 'image/' | 'video/';
};

const rootFolder: FileData = {
  id: 'root',
  name: 'root',
  isDir: true,
};

// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
setChonkyDefaults({ iconComponent: ChonkyIconFA });

const FileManager: FC<Props> = ({
  width,
  height,
  mimeType,
  insertFileIntoEditor,
}) => {
  const [currentFolder, setcurrentFolder] = useState<FileData>(rootFolder);
  const { data: files, queryKey } = useFetchFilesQuery(
    currentFolder.id,
    mimeType,
  );
  const folderChain = useFolderChain(currentFolder);
  const handleFileAction = useFileActionHandler(
    currentFolder,
    setcurrentFolder,
    queryKey,
    insertFileIntoEditor,
    mimeType,
  );

  return (
    <div
      style={{
        height: height ?? '400px',
        width: width ?? '100%',
      }}
    >
      {/* eslint-disable-next-line @typescript-eslint/ban-ts-comment */}
      {/* @ts-ignore */}
      <FullFileBrowser
        files={files || [null]}
        fileActions={[
          ChonkyCustomActions.UploadFiles,
          ChonkyCustomActions.CreateFolder,
          ChonkyCustomActions.RenameFiles,
          ChonkyCustomActions.DeleteFiles,
        ]}
        disableDefaultFileActions={[
          ChonkyActions.OpenSelection.id,
          ChonkyActions.ToggleHiddenFiles.id,
        ]}
        disableDragAndDrop={true}
        folderChain={folderChain}
        onFileAction={handleFileAction}
      />
    </div>
  );
};

export default FileManager;
