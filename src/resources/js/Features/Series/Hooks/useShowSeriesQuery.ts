import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Series } from '../Types';

const fetchSeries = (id: number) => {
  return axios.get(route('series.show', id)).then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Series, Error>>[2],
];

export const useShowSeriesQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().series.show(id);
  const query = useQuery<Series, Error>(
    queryKey,
    () => fetchSeries(id),
    options,
  );

  return { ...query, queryKey };
};
