import { useMutation, UseMutationResult } from 'react-query';
import axios, { AxiosResponse } from 'axios';
import { UseToastOptions } from '@chakra-ui/react';
import { useToast } from '@chakra-ui/react';
import { Label } from '@/Features/Label/Types';

interface SortLabelResponse {
  success: boolean;
  message: string;
}

export const useSortLabelMutationDND = (): UseMutationResult<SortLabelResponse, Error, Label[]> => {
  const toast = useToast();

  const useMoveMutation = () =>
    useMutation<SortLabelResponse, Error, Label[]>({
      mutationFn: (data: Label[]) =>
        axios
          .patch<SortLabelResponse>(route(`label.sort`), { data })
          .then((result: AxiosResponse<SortLabelResponse>) => {
            console.log(result);
            return result.data;
          }),
      onSuccess: () => {
        toast({
          title: 'Saved the sorting order successfully',
          status: 'success',
        } as UseToastOptions);
      },
      onError: () => {
        toast({ title: 'Failed to save the sorting order', status: 'error' } as UseToastOptions);
      },
    });

  return useMoveMutation();
};
