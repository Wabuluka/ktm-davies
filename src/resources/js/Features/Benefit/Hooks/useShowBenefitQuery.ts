import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Benefit } from '@/Features/Benefit';

const fetchBenefit = (id: number) => {
  return axios
    .get(route('benefits.show', id))
    .then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Benefit, Error>>[2],
];

export const useShowBenefitQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().benefits.show(id);
  const query = useQuery<Benefit, Error>(
    queryKey,
    () => fetchBenefit(id),
    options,
  );

  return { ...query, queryKey };
};
