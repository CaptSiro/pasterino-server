<?php

namespace models;

import("oakbase");
import("retval");
import("base64");

use Exc;
use Exception;
use NotFoundExc;
use OakBase\Database;
use OakBase\MixedIndexingException;
use OakBase\Param;
use Result;
use function OakBase\param;


class Session {
    public string $id, $expires;
    public int $users_id;


    static function create(Param $session_id, Param $user_id): Result {
        $res = self::delete_old();

        if ($res->isFailure()) {
            return $res;
        }

        $expires = self::generate_expires();

        try {
            return success(Database::get()->statement(
                "INSERT INTO sessions (id, expires, users_id)
              VALUE ($session_id, $expires, $user_id)"
            ));
        } catch (MixedIndexingException $e) {
            return fail_e($e);
        }
    }


    static function refresh(Param $session_id): Result {
        try {
            $expires = self::generate_expires();

            Database::get()->statement(
                "UPDATE sessions SET expires = $expires WHERE id = $session_id"
            );

            return success($expires->value());
        } catch (MixedIndexingException $e) {
            return fail_e($e);
        }
    }


    static function generate_expires(): Param {
        return param(date("Y-m-d H:i:s", time() + SESSION_EXPIRATION_TIME));
    }


    static function generate_id(): Result {
        try {
            $id = gen_base64();
        } catch (Exception $e) {
            return fail(new Exc($e->getMessage()));
        }

        $try = 1;

        do {
            $try++;
            $exists = self::by_id(param($id));

            if ($exists->isFailure()) {
                return success($id);
            }

            if ($try > SESSION_MAX_TRIES) {
                return fail(new Exc("Could not create session."));
            }

            try {
                $id = gen_base64();
            } catch (Exception $e) {
                return fail(new Exc($e->getMessage()));
            }
        } while (true);
    }


    static function by_id(Param $id): Result {
        try {
            $session = Database::get()->fetch(
                "SELECT id, expires, users_id
          FROM sessions
          WHERE id = $id
              AND expires > CURRENT_TIMESTAMP"
            );
        } catch (MixedIndexingException $e) {
            return fail(new Exc($e->getMessage()));
        }

        if ($session === false || $session === null) {
            return fail(new NotFoundExc("Session was not found with id: " . $id->value()));
        }

        return success($session);
    }


    public static function delete_old(): Result {
        try {
            return success(
                Database::get()->statement(
                    "DELETE FROM sessions WHERE expires < CURRENT_TIMESTAMP"
                )
            );
        } catch (MixedIndexingException $e) {
            return fail_e($e);
        }
    }
}