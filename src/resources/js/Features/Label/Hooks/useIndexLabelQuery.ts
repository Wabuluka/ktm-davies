import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Label } from '../Types';

export type QueryParams = {
  name?: string;
};

const fetchLabel = (queryParams?: QueryParams) => {
  return axios
    .get(route('label.index'), { params: queryParams })
    .then((response) => response.data);
};

export const useIndexLabelQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.label.filtered(queryParams)
    : queryKeys.label.all;
  const query = useQuery<Label[], Error>(queryKey, () =>
    fetchLabel(queryParams),
  );

  return { ...query, queryKey };
};
