import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Character } from '@/Features/Character';

const fetchCharacter = (id: number) => {
  return axios
    .get(route('characters.show', id))
    .then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Character, Error>>[2],
];

export const useShowCharacterQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().characters.show(id);
  const query = useQuery<Character, Error>(
    queryKey,
    () => fetchCharacter(id),
    options,
  );
  return { ...query, queryKey };
};
