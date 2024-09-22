import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreationTypeEventListener } from '../../CreationType/Contexts/CreationTypeEventCallbackContext';
import { CreationType } from '../Types';

export const useSortCreationTypeMutation = () => {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creationTypes.all;
  const { onOrderDownSuccess, onOrderUpSuccess } =
    useCreationTypeEventListener();
  const useMoveMutation = (direction: 'up' | 'down') =>
    useMutation<AxiosResponse<CreationType>, AxiosError, string>({
      mutationFn: (name: CreationType['name']) =>
        axios
          .patch(route(`api.creation-types.move_${direction}`, name))
          .then((result) => result.data),
      onSuccess: ({ data }) => {
        toast({ title: 'Saved the sorting order', status: 'success' });
        queryClient.invalidateQueries(queryKey);
        if (direction === 'up') {
          onOrderUpSuccess?.(data);
        }
        if (direction === 'down') {
          onOrderDownSuccess?.(data);
        }
      },
      onError: () => {
        toast({ title: 'Failed to save the sorting order', status: 'error' });
      },
    });

  return {
    moveUpMutation: useMoveMutation('up'),
    moveDownMutation: useMoveMutation('down'),
  };
};
