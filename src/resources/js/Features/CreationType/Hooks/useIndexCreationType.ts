import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { CreationType } from '../Types';

const fetchCreationTypes = () => {
  return axios
    .get(route('api.creation-types.index'))
    .then((response) => response.data);
};

export function useIndexCreationType(
  options: Parameters<typeof useQuery<CreationType[], Error>>[2] = {
    staleTime: 0,
  },
) {
  const queryKey = useQueryKeys().creationTypes.all;
  const query = useQuery<CreationType[], Error>(
    queryKey,
    fetchCreationTypes,
    options,
  );

  return query;
}
