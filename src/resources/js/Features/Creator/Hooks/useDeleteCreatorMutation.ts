import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreatorEventListener } from '../Contexts/CreatorEventListnerContext';

export const useDeleteCreatorMutation = () => {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creators.all;
  const { onDeleteSuccess } = useCreatorEventListener();

  return useMutation<AxiosResponse, AxiosError, string | number>({
    mutationFn: (id) => {
      return axios.delete(route('api.creators.destroy', id));
    },
    onSuccess: (_, id) => {
      toast({ title: 'Deleted successfully', status: 'success' });
      queryClient.invalidateQueries(queryKey);
      onDeleteSuccess?.(String(id));
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
