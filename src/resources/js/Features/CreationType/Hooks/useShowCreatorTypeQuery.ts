import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { CreationType } from '../Types';

type Args = [
  id: string | number,
  options?: Parameters<typeof useQuery<CreationType, Error>>[2],
];

const fetchCreationType = (id: string | number) => {
  return axios
    .get(route('api.creation-types.show', id))
    .then((response) => response.data);
};

export const useShowCreationTypeQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().creationTypes.show(id);
  const query = useQuery<CreationType, Error>(
    queryKey,
    () => fetchCreationType(id),
    options,
  );

  return query;
};
