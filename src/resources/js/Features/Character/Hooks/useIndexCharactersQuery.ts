import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { Character } from '@/Features/Character';
import axios from 'axios';
import { useQuery } from 'react-query';

export type QueryParams = {
  name?: string;
  seriesId?: number | undefined;
  currentIndex?: number;
};

const fetchCharacters = (queryParams?: QueryParams) => {
  return axios
    .get(route('characters.index'), {
      params: {
        name: queryParams?.name,
        seriesId: queryParams?.seriesId,
        page: queryParams?.currentIndex,
      },
    })
    .then((response) => response.data);
};

export const useIndexCharactersQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.characters.filtered(queryParams)
    : queryKeys.characters.all;
  const query = useQuery<[Character[], number], Error>(queryKey, () =>
    fetchCharacters(queryParams),
  );

  const characters = query.data?.[0];
  const lastPage = query.data?.[1] ?? 1;

  return { ...query, queryKey, characters, lastPage };
};
