import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreatorEventListener } from '../Contexts/CreatorEventListnerContext';
import { Creator, CreatorFormData } from '../Types';

export function useCreateCreatorMutation() {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creators.all;
  const { onStoreSuccess } = useCreatorEventListener();

  return useMutation<AxiosResponse<Creator>, AxiosError, CreatorFormData>({
    mutationFn: ({ name, name_kana }) => {
      return axios.post(route('api.creators.store'), { name, name_kana });
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
