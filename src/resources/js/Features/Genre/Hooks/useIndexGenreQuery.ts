import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Genre } from '../Types';

export type QueryParams = {
  name?: string;
};

const fetchGenre = async (queryParams?: QueryParams) => {
  return axios
    .get(route('genre.index'), { params: queryParams })
    .then((response) => response.data);
};

export const useIndexGenreQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.genre.filtered(queryParams)
    : queryKeys.genre.all;
  const query = useQuery<Genre[], Error>(queryKey, () =>
    fetchGenre(queryParams),
  );

  return { ...query, queryKey };
};
