import { InsertImagePayload } from '@/Features/RichTextEditor/plugins/ImagesPlugin';
import { FC, useState } from 'react';
import { useDrag, useDrop } from 'react-dnd';
import { useFetchFilesQuery } from '../Hooks/useFetchFilesQuery';
import { useFileActionHandler } from '../Hooks/useFileActionHandler';
import { useFolderChain } from '../Hooks/useFolderChian';


type Props = {
  width?: string;
  height?: string;
  insertFileIntoEditor?: (payload: InsertImagePayload) => void;
  mimeType?: 'image/' | 'video/';
};

type FileData = {
  id: string;
  name: string;
  isDir?: boolean;
};

const rootFolder: FileData = {
  id: 'root',
  name: 'root',
  isDir: true,
};

const File: FC<{ file: FileData; onFileClick: (file: FileData) => void }> = ({ file, onFileClick }) => {
  const [, drag] = useDrag({
    type: 'FILE',
    item: { id: file.id, name: file.name, isDir: file.isDir },
  });

  return (
    <div ref={drag} onClick={() => onFileClick(file)} style={{ padding: '8px', border: '1px solid #ccc', margin: '4px' }}>
      {file.name}
    </div>
  );
};

const FolderDropArea: FC<{ onDropFile: (file: FileData) => void }> = ({ onDropFile, children }) => {
  const [, drop] = useDrop({
    accept: 'FILE',
    drop: (item: FileData) => onDropFile(item),
  });

  return (
    <div ref={drop} style={{ border: '2px dashed #ccc', padding: '16px', minHeight: '200px' }}>
      {children}
    </div>
  );
};

const FileManager: FC<Props> = ({ width, height, mimeType, insertFileIntoEditor }) => {
  const [currentFolder, setCurrentFolder] = useState<FileData>(rootFolder);
  const { data: files, queryKey } = useFetchFilesQuery(currentFolder.id, mimeType);
  const folderChain = useFolderChain(currentFolder);
  const handleFileAction = useFileActionHandler(
    currentFolder,
    setCurrentFolder,
    queryKey,
    insertFileIntoEditor,
    mimeType,
  );

  const handleFileClick = (file: FileData) => {
    if (file.isDir) {
      setCurrentFolder(file);
    } else if (insertFileIntoEditor) {
      insertFileIntoEditor({ src: file.id, alt: file.name });
    }
  };

  const handleDropFile = (droppedFile: FileData) => {
    console.log(`File dropped: ${droppedFile.name}`);
  };

  return (
    <div style={{ height: height ?? '400px', width: width ?? '100%' }}>
      <FolderDropArea onDropFile={handleDropFile}>
        <div style={{ display: 'flex', flexDirection: 'column' }}>
          {files?.map((file) => (
            <File key={file.id} file={file} onFileClick={handleFileClick} />
          ))}
        </div>
      </FolderDropArea>
    </div>
  );
};

export default FileManager;
