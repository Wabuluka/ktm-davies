import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreationTypeEventListener } from '../Contexts/CreationTypeEventCallbackContext';
import { CreationType, CreationTypeFormData } from '../Types';

export function useCreateCreationTypeMutation() {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creationTypes.all;
  const { onStoreSuccess } = useCreationTypeEventListener();

  return useMutation<
    AxiosResponse<CreationType>,
    AxiosError,
    CreationTypeFormData
  >({
    mutationFn: ({ name }) => {
      return axios.post(route('api.creation-types.store'), { name });
    },
    onSuccess: ({ data }) => {
      toast({ title: 'Saved successfully', status: 'success' });
      queryClient.invalidateQueries(queryKey);
      onStoreSuccess?.(data);
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
}
