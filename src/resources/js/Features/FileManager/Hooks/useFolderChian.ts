import { FileArray, FileData } from 'chonky';
import { useState, useEffect } from 'react';

export const useFolderChain = (currentFolder: FileData): FileArray => {
  const [folderChain, setFolderChain] = useState<FileArray>([]);

  useEffect(() => {
    setFolderChain((prevFolder) => {
      const prevIndex = prevFolder.findIndex(
        (folder) => folder?.id === currentFolder.id,
      );

      return prevIndex === -1
        ? prevFolder.concat(currentFolder)
        : prevFolder.slice(0, prevIndex).concat(currentFolder);
    });
  }, [currentFolder]);

  return folderChain;
};
