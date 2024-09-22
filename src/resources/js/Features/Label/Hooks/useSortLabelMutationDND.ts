import { useMutation } from 'react-query';
import axios from 'axios';
import {  } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useSortLabelMutationDND = () => {
  const toast = useToast();

  const useMoveMutation = () =>
    useMutation({
      mutationFn: (data) =>
        axios
          .patch(route(`label/sort`, {order: data}))
          .then((result) => {
            console.log(result)
            result.data
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

  return  useMoveMutation()
};
