import { useMutation, UseMutationResult } from 'react-query';
import axios, { AxiosResponse } from 'axios';
import { UseToastOptions } from '@chakra-ui/react';
import { useToast } from '@chakra-ui/react';
import { BlockOnBookForm } from '@/Features/Block/Types';

interface SortLabelResponse {
  // create  response structure here if known
  success: boolean;
  message: string;
}
export const useSortBlockMutationDND = (): UseMutationResult<
  SortLabelResponse,
  Error,
  BlockOnBookForm
> => {
  const toast = useToast();

  const useMoveMutation = () =>
    useMutation<SortLabelResponse, Error, BlockOnBookForm>({
      mutationFn: (data: BlockOnBookForm) =>
        axios
          .post<SortLabelResponse>(route(`block.sort`, { data }))
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
        // console.log(Error);
        toast({
          title: 'Failed to save the sorting order for blocks',
          status: 'error',
        } as UseToastOptions);
      },
    });

  return useMoveMutation();
};
