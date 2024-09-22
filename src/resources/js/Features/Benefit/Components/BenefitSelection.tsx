import { Benefit } from '@/Features/Benefit';
import { useShowBenefitQuery } from '@/Features/Benefit/Hooks/useShowBenefitQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { Box } from '@chakra-ui/react';
import { useEffect } from 'react';

type Props = {
  benefitId: number;
  handleUpdate: (benefit: Benefit) => void;
};

export function BenefitSelection({ benefitId, handleUpdate }: Props) {
  const { data, isLoading, isError } = useShowBenefitQuery(benefitId);

  useEffect(() => {
    if (data) {
      handleUpdate(data);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data]);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Box> {data.name} </Box>;
}
