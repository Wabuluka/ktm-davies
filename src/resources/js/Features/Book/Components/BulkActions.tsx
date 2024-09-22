import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  AlertDialog,
  AlertDialogBody,
  AlertDialogCloseButton,
  AlertDialogContent,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogOverlay,
  Box,
  BoxProps,
  Button,
  ButtonGroup,
  HStack,
  Text,
  useDisclosure,
  useToast,
} from '@chakra-ui/react';
import { router } from '@inertiajs/react';
import { useRef } from 'react';
import { BsGlobe, BsTrash } from 'react-icons/bs';
import { Book } from '../Types';

type Props = Omit<BoxProps, 'children'> & {
  books: Book[];
  onPublishSuccess?: () => void;
  onDeleteSuccess?: () => void;
};

export function BulkActions({
  books,
  onPublishSuccess,
  onDeleteSuccess,
  ...props
}: Props) {
  const {
    isOpen: isDeleteAlertOpen,
    onOpen: onDeleteAlertOpen,
    onClose: onDeleteAlertClose,
  } = useDisclosure();
  const {
    isOpen: isPublishAlertOpen,
    onOpen: onPublishAlertOpen,
    onClose: onPublishAlertClose,
  } = useDisclosure();
  const cancelRef = useRef(null);
  const toast = useToast();

  const getHeaders = () => {
    return {
      searchParams: location.search ? location.search.substring(1) : '',
    };
  };

  const getBookIds = () => {
    return books.map((book) => book.id);
  };

  const onPublish = () => {
    const onSuccess = () => {
      toast({
        title: 'Book has been published',
        status: 'success',
      });
      onPublishSuccess?.();
    };
    if (books.length === 1) {
      router.patch(route('books.publish', books[0].id), undefined, {
        headers: getHeaders(),
        onSuccess,
      });
    } else if (books.length > 1) {
      router.patch(
        route('books.publish-many'),
        { ids: getBookIds() },
        { headers: getHeaders(), onSuccess },
      );
    }
  };

  const onDelete = () => {
    const onSuccess = () => {
      toast({
        title: 'Book has been Deleted',
        status: 'success',
      });
      onDeleteSuccess?.();
    };
    if (books.length === 1) {
      router.delete(route('books.destroy', books[0].id), {
        headers: getHeaders(),
        onSuccess,
      });
    } else if (books.length > 1) {
      router.delete(route('books.destroy-many'), {
        data: { ids: getBookIds() },
        headers: getHeaders(),
        onSuccess,
      });
    }
  };

  return (
    <Box bg="gray.50" p={4} borderRadius={12} {...props}>
      <HStack>
        <Text>Selected Book(s)...</Text>
        <HStack as={ButtonGroup}>
          <Button
            leftIcon={<BsGlobe />}
            bg="green.100"
            color="green.800"
            variant="outline"
            onClick={onPublishAlertOpen}
          >
            Publish
          </Button>
          <AlertDialog
            motionPreset="slideInBottom"
            leastDestructiveRef={cancelRef}
            onClose={onPublishAlertClose}
            isOpen={isPublishAlertOpen}
            isCentered
          >
            <AlertDialogOverlay />
            <AlertDialogContent>
              <AlertDialogHeader>
                Are you sure to pulish this book?
              </AlertDialogHeader>
              <AlertDialogBody>
                {books.map((book, index) => (
                  <div key={index}>{book.title}</div>
                ))}
              </AlertDialogBody>
              <AlertDialogCloseButton />
              <AlertDialogFooter>
                <ButtonGroup>
                  <Button ref={cancelRef} onClick={onPublishAlertClose}>
                    No
                  </Button>
                  <PrimaryButton
                    onClick={() => {
                      onPublish();
                      onPublishAlertClose();
                    }}
                  >
                    Yes
                  </PrimaryButton>
                </ButtonGroup>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
          <Button
            bg="red.100"
            color="red.800"
            leftIcon={<BsTrash />}
            onClick={onDeleteAlertOpen}
          >
            Delete
          </Button>
          <AlertDialog
            motionPreset="slideInBottom"
            leastDestructiveRef={cancelRef}
            onClose={onDeleteAlertClose}
            isOpen={isDeleteAlertOpen}
            isCentered
          >
            <AlertDialogOverlay />
            <AlertDialogContent>
              <AlertDialogHeader>
                Are you sure to delete this book?
              </AlertDialogHeader>
              <AlertDialogBody>
                {books.map((book, index) => (
                  <div key={index}>{book.title}</div>
                ))}
              </AlertDialogBody>
              <AlertDialogCloseButton />
              <AlertDialogFooter>
                <ButtonGroup>
                  <Button ref={cancelRef} onClick={onDeleteAlertClose}>
                    No
                  </Button>
                  <DangerButton
                    onClick={() => {
                      onDelete();
                      onDeleteAlertClose();
                    }}
                  >
                    Yes
                  </DangerButton>
                </ButtonGroup>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
        </HStack>
      </HStack>
    </Box>
  );
}
