import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreationTypeEventListener } from '../Contexts/CreationTypeEventCallbackContext';

export const useDeleteCreationTypeMutation = () => {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creationTypes.all;
  const { onDeleteSuccess } = useCreationTypeEventListener();

  return useMutation<AxiosResponse, AxiosError, string>({
    mutationFn: (name) => {
      return axios.delete(route('api.creation-types.destroy', name));
    },
    onSuccess: (_, name) => {
      toast({ title: 'Deleted successfully', status: 'success' });
      queryClient.invalidateQueries(queryKey);
      onDeleteSuccess?.(name);
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
