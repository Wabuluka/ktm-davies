import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreationTypeEventListener } from '../../CreationType/Contexts/CreationTypeEventCallbackContext';
import { CreationType, CreationTypeFormData } from '../Types';

type Variables = { currentName: string } & CreationTypeFormData;

export function useEditCreationTypeMutation() {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creationTypes.all;
  const { onUpdateSuccess } = useCreationTypeEventListener();

  return useMutation<AxiosResponse<CreationType>, AxiosError, Variables>({
    mutationFn: ({ currentName, name }) => {
      return axios.patch(route('api.creation-types.update', currentName), {
        name,
      });
    },
    onSuccess: ({ data }, { currentName }) => {
      toast({ title: 'Saved successfully', status: 'success' });
      queryClient.invalidateQueries(queryKey);
      onUpdateSuccess?.(data, currentName);
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
}
