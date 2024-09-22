import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Genre } from '../Types';

const fetchGenre = async (id: number) => {
  return axios.get(route('genre.show', id)).then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Genre, Error>>[2],
];

export const useShowGenreQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().genre.show(id);
  const query = useQuery<Genre, Error>(queryKey, () => fetchGenre(id), options);
  return { ...query, queryKey };
};
