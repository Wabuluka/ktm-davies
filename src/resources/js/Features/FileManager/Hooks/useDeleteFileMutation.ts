import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { FileData } from 'chonky';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';

export const useDeleteFileMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse[], AxiosError, { files: FileData[] }>({
    mutationFn: ({ files }) => {
      const fileIds = files
        .filter((f) => !f.isDir)
        .map((f) => f.id.match(/\d+/)?.toString());
      const folderIds = files
        .filter((f) => f.isDir)
        .map((f) => f.id.match(/\d+/)?.toString());
      const fileIdsExists = !!fileIds.length;
      const folderIdsExists = !!folderIds.length;
      const promises: Promise<AxiosResponse>[] = [];

      if (fileIdsExists) {
        const deleteFilePromise = axios.delete(route('files.destroy-many'), {
          data: { fileIds },
        });
        promises.push(deleteFilePromise);
      }
      if (folderIdsExists) {
        const deleteFolderPromise = axios.delete(
          route('folders.destroy-many'),
          {
            data: { folderIds },
          },
        );
        promises.push(deleteFolderPromise);
      }

      return Promise.all(promises);
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: (error) => {
      const message = isLaravelValidationError(error)
        ? error?.response?.data?.message
        : '';
      toast({
        title: 'Failed to delete',
        description: message,
        status: 'error',
      });
    },
  });
};
