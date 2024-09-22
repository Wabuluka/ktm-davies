import {
  Alert,
  AlertIcon,
  AlertTitle,
  AlertDescription,
} from '@chakra-ui/react';

export const DataFetchError = () => {
  return (
    <Alert status="error">
      <AlertIcon />
      <AlertTitle>データを取得できませんでした。</AlertTitle>
      <AlertDescription>後でもう一度お試しください。</AlertDescription>
    </Alert>
  );
};
