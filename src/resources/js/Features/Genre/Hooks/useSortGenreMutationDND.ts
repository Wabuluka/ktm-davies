import { useMutation } from 'react-query';
import axios from 'axios';
import { Genre } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useSortGenreMutationDND = () => {
  const toast = useToast();

  const useMoveMutation = () =>
    // console.log(direction)
    useMutation({
      mutationFn: (data: Genre) =>
        axios.post(route(`genre.sort`, { data })).then((result) => {
          result.data;
        }),
      onSuccess: () => {
        toast({
          title: 'Saved the sorting order successfully',
          status: 'success',
        });
      },
      onError: () => {
        toast({ title: 'Failed to save the sorting order', status: 'error' });
      },
    });

  return useMoveMutation();
};
