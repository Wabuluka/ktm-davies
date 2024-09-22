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
  FormControl,
  FormErrorMessage,
  FormLabel,
  HStack,
  Input,
  Text,
  useDisclosure,
  VStack,
} from '@chakra-ui/react';
import { useRef } from 'react';
import { useDeleteCurrentUser } from '../Hooks/useDeleteCurrentUser';

export default function DeleteUserForm() {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const passwordInputRef = useRef<HTMLInputElement>(null);
  const cancelRef = useRef(null);
  const { data, setData, deleteUser, reset, errors, clearErrors, processing } =
    useDeleteCurrentUser();

  function handleDialogClose() {
    reset();
    clearErrors();
    onClose();
  }

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    deleteUser({
      preserveScroll: true,
      onError: () =>
        passwordInputRef.current && passwordInputRef.current.focus(),
      onFinish: () => reset(),
    });
  }

  return (
    <Box>
      <DangerButton onClick={onOpen}>Delete account</DangerButton>
      <AlertDialog
        motionPreset="slideInBottom"
        leastDestructiveRef={cancelRef}
        onClose={handleDialogClose}
        isOpen={isOpen}
        isCentered
      >
        <AlertDialogOverlay
          bg="blackAlpha.300"
          backdropFilter="blur(10px) hue-rotate(90deg)"
        />
        <AlertDialogContent as="form" onSubmit={handleSubmit}>
          <AlertDialogHeader>⚠️ アカウントを削除する</AlertDialogHeader>
          <AlertDialogBody>
            <VStack spacing={4} px={4}>
              <Text>
                この操作は取り消すことができません。あなたのアカウント情報は完全に削除されます。
                <br />
                操作を完了させるには、パスワードを入力してください。
              </Text>
              <FormControl isInvalid={!!errors.password} isRequired>
                <FormLabel>パスワード</FormLabel>
                <Input
                  ref={passwordInputRef}
                  type="password"
                  name="password"
                  autoComplete="current-password"
                  value={data.password}
                  onChange={(e) => setData('password', e.target.value)}
                />
                <FormErrorMessage>{errors.password}</FormErrorMessage>
              </FormControl>
            </VStack>
          </AlertDialogBody>
          <AlertDialogCloseButton />
          <AlertDialogFooter>
            <HStack>
              <Button ref={cancelRef} onClick={handleDialogClose}>
                キャンセル
              </Button>
              <DangerButton
                type="submit"
                isLoading={processing}
                isDisabled={!data.password}
              >
                削除
              </DangerButton>
            </HStack>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Box>
  );
}
