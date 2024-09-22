import { LengthAwarePaginator } from '@/Api/Types';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useState } from 'react';
import { useQuery } from 'react-query';
import { Creator } from '../Types';

export type IndexCreatorQueryParams = {
  keyword?: string;
  page?: number;
};

const fetchCreators = (params?: IndexCreatorQueryParams) => {
  return axios
    .get(route('api.creators.index'), {
      params,
    })
    .then((response) => response.data);
};

export function useIndexCreatorQuery(
  params?: IndexCreatorQueryParams,
  options: Parameters<
    typeof useQuery<LengthAwarePaginator<Creator>, Error>
  >[2] = {
    staleTime: 0,
  },
) {
  const [queryParams, setQueryParams] = useState<IndexCreatorQueryParams>(
    params || {
      page: 1,
      keyword: '',
    },
  );
  const queryKeys = useQueryKeys();
  const queryKey = queryKeys.creators.index(queryParams);
  const query = useQuery<LengthAwarePaginator<Creator>, Error>(
    queryKey,
    () => fetchCreators(queryParams),
    options,
  );

  return { ...query, setQueryParams, queryKey };
}
