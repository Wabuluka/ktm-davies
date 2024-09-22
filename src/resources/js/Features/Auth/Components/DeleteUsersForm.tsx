import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import {
  AlertDialog,
  AlertDialogBody,
  AlertDialogCloseButton,
  AlertDialogContent,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogOverlay,
  Box,
  Button,
  HStack,
  useDisclosure,
  useToast,
} from '@chakra-ui/react';
import { useRef } from 'react';
import { useCurrentUser } from '../Hooks/useCurrentUser';
import { useDeleteUser } from '../Hooks/useDeleteUser';

type Props = {
  userId: number;
  username: string;
};

export default function DeleteUsersForm({ userId, username }: Props) {
  const { deleteUser, processing } = useDeleteUser(userId);
  const { id: currentUserId } = useCurrentUser();
  const { isOpen, onOpen, onClose } = useDisclosure();
  const cancelRef = useRef(null);
  const toast = useToast({
    title: 'ユーザーをDeleted successfully。',
    status: 'success',
  });

  if (userId == currentUserId) {
    return null;
  }

  const handleSubmit = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();

    deleteUser({ preserveState: true, onSuccess: () => toast() });
  };

  return (
    <Box>
      <DangerButton onClick={onOpen}>削除</DangerButton>
      <AlertDialog
        motionPreset="slideInBottom"
        leastDestructiveRef={cancelRef}
        onClose={onClose}
        isOpen={isOpen}
        isCentered
      >
        <AlertDialogOverlay
          bg="blackAlpha.300"
          backdropFilter="blur(10px) hue-rotate(90deg)"
        />
        <AlertDialogContent as="form" onSubmit={handleSubmit}>
          <AlertDialogHeader>⚠️ {username} を削除しますか？</AlertDialogHeader>
          <AlertDialogBody>
            この操作は取り消すことができません。
          </AlertDialogBody>
          <AlertDialogCloseButton />
          <AlertDialogFooter>
            <HStack>
              <Button ref={cancelRef} onClick={onClose}>
                キャンセル
              </Button>
              <DangerButton type="submit" isLoading={processing}>
                削除
              </DangerButton>
            </HStack>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Box>
  );
}
