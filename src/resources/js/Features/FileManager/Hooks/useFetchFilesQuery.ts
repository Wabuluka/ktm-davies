import FileManager from '@/Features/FileManager/Components/FileManager';
import axios from 'axios';
import { FileArray } from 'chonky';
import { useQuery } from 'react-query';

export type QueryParams = {
  mimeType?: React.ComponentProps<typeof FileManager>['mimeType'];
};

const fetchFiles = (
  folderId: string,
  mimeType?: React.ComponentProps<typeof FileManager>['mimeType'],
): Promise<FileArray> => {
  return axios
    .get(
      folderId === 'root'
        ? route('folders.index')
        : route('folders.show', folderId),
      {
        params: { mimeType },
      },
    )
    .then((response) => response.data);
};

export const useFetchFilesQuery = (
  folderId: string,
  mimeType?: React.ComponentProps<typeof FileManager>['mimeType'],
) => {
  const queryKey = ['files', { folderId, mimeType }];
  const query = useQuery(queryKey, () => fetchFiles(folderId, mimeType));

  return { ...query, queryKey };
};
