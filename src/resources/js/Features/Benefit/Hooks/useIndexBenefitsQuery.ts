import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Benefit } from '@/Features/Benefit';

export type QueryParams = {
  name?: string;
  currentIndex?: number;
};

const fetchBenefits = (queryParams?: QueryParams) => {
  return axios
    .get(route('benefits.index'), {
      params: { name: queryParams?.name, page: queryParams?.currentIndex },
    })
    .then((response) => response.data);
};

export const useIndexBenefitsQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.benefits.filtered(queryParams)
    : queryKeys.benefits.all;
  const query = useQuery<[Benefit[], number], Error>(queryKey, () =>
    fetchBenefits(queryParams),
  );

  const benefits = query.data?.[0];
  const lastPage = query.data?.[1] ?? 1;

  return { ...query, queryKey, benefits, lastPage };
};
