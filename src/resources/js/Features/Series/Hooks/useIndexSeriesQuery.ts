import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Series } from '../Types';
import { LengthAwarePaginator } from '@/Api/Types';

export type QueryParams = {
  name?: string;
  page?: number;
};

const fetchSeries = (queryParams?: QueryParams) => {
  return axios
    .get(route('series.index'), { params: queryParams })
    .then((response) => response.data);
};

export const useIndexSeriesQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.series.filtered(queryParams)
    : queryKeys.series.all;
  const query = useQuery<LengthAwarePaginator<Series>, Error>(queryKey, () =>
    fetchSeries(queryParams),
  );

  return { ...query, queryKey };
};
