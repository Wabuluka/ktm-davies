import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Creator } from '../Types';

type Args = [
  id: string | number,
  options?: Parameters<typeof useQuery<Creator, Error>>[2],
];

const fetchCreator = (id: string | number) => {
  return axios
    .get(route('api.creators.show', id))
    .then((response) => response.data);
};

export const useShowCreatorQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().creators.show(id);
  const query = useQuery<Creator, Error>(
    queryKey,
    () => fetchCreator(id),
    options,
  );

  return query;
};
