import { InsertImagePayload } from '@/Features/RichTextEditor/plugins/ImagesPlugin';
import { FC, useState, useCallback, useRef } from 'react';
import { useDrop } from 'react-dnd';

type Props = {
  width?: string;
  height?: string;
  insertFileIntoEditor?: (payload: InsertImagePayload) => void;
};

type FileData = {
  id: string;
  name: string;
  preview: string;
};

const File: FC<{ file: FileData; onInsert: (file: FileData) => void }> = ({
  file,
  onInsert,
}) => {
  return (
    <div
      style={{
        padding: '8px',
        margin: '4px',
        border: '1px solid #ccc',
        cursor: 'pointer',
      }}
      onClick={() => onInsert(file)}
    >
      <img
        src={file.preview}
        alt={file.name}
        style={{ maxWidth: '100px', maxHeight: '100px' }}
      />
      {/* <div>{file.name}</div> */}
    </div>
  );
};

const FileManager: FC<Props> = ({ width, height, insertFileIntoEditor }) => {
  const [files, setFiles] = useState<FileData[]>([]);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFile = useCallback((file: File) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        setFiles((prevFiles) => [
          ...prevFiles,
          {
            id: file.name,
            name: file.name,
            preview: e.target?.result as string,
          },
        ]);
      };
      reader.readAsDataURL(file);
    }
  }, []);

  const [, drop] = useDrop({
    accept: ['__NATIVE_FILE__'],
    drop: (item: { files: File[] }) => {
      item.files.forEach(handleFile);
    },
  });

  const handleBrowse = useCallback(() => {
    fileInputRef.current?.click();
  }, []);

  const handleFileInputChange = useCallback(
    (event: React.ChangeEvent<HTMLInputElement>) => {
      const files = event.target.files;
      if (files) {
        Array.from(files).forEach(handleFile);
      }
    },
    [handleFile],
  );

  const handleInsertFile = useCallback(
    (file: FileData) => {
      if (insertFileIntoEditor) {
        insertFileIntoEditor({
          src: file.preview,
          alt: file.name,
        });
      }
    },
    [insertFileIntoEditor],
  );

  return (
    <div style={{ height, width, border: '1px solid #ccc', padding: '16px' }}>
      <div
        ref={drop}
        style={{
          padding: '16px',
          border: '2px dashed #ccc',
          minHeight: '200px',
          display: 'flex',
          flexWrap: 'wrap',
          alignContent: 'flex-start',
        }}
      >
        {files.map((file) => (
          <File key={file.id} file={file} onInsert={handleInsertFile} />
        ))}
        {files.length === 0 && (
          <div style={{ padding: '16px', textAlign: 'center', width: '100%' }}>
            Drag and drop image files here or use the browse button
          </div>
        )}
      </div>
      <button onClick={handleBrowse} style={{ marginTop: '16px' }}>
        Browse Files
      </button>
      <input
        type="file"
        ref={fileInputRef}
        style={{ display: 'none' }}
        onChange={handleFileInputChange}
        accept="image/*"
        multiple
      />
    </div>
  );
};

export default FileManager;
