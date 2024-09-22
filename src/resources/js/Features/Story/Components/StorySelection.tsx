import { useShowStoryQuery } from '@/Features/Story/Hooks/useShowStoryQuery';
import { Story } from '@/Features/Story';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { Box } from '@chakra-ui/react';
import { useEffect, useRef } from 'react';

type Props = {
  storyId: number;
  handleUpdate: (story: Story) => void;
};

export function StorySelection({ storyId, handleUpdate }: Props) {
  const { data, isLoading, isError } = useShowStoryQuery(storyId);

  const handleUpdateRef = useRef(handleUpdate);

  useEffect(() => {
    if (data) {
      handleUpdateRef.current(data);
    }
  }, [data]);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Box> {data.title} </Box>;
}
